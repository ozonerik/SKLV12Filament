<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\Major;
use App\Models\SchoolYear;
use App\Models\Skl;
use App\Models\Student;
use App\Models\Subject;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use OpenSpout\Reader\XLSX\Reader;
use RuntimeException;

class LulusanExcelImportService
{
	/**
	 * @return array<string, int>
	 */
	public function preview(string $filePath): array
	{
		return $this->process($filePath, persist: false);
	}

	/**
	 * @return array<string, int>
	 */
	public function import(string $filePath): array
	{
		return $this->process($filePath, persist: true);
	}

	/**
	 * @return array<string, int>
	 */
	protected function process(string $filePath, bool $persist): array
	{
		$rows = $this->readRows($filePath);

		if (count($rows) < 4) {
			throw new InvalidArgumentException('Format file tidak valid. Minimal harus berisi header dan data mulai baris 4.');
		}

		$columnMap = $this->resolveColumns($rows[1]);
		$subjectColumnMap = $this->resolveSubjectColumns(
			$rows[2],
			$columnMap['subject_start_column']
		);

		$majorMap = Major::query()->get()->keyBy(fn (Major $major) => strtoupper(trim((string) $major->kode_jurusan)));
		$schoolYearMap = SchoolYear::query()->get()->keyBy(fn (SchoolYear $schoolYear) => strtoupper(trim((string) $schoolYear->name)));

		$result = [
			'students_created' => 0,
			'students_updated' => 0,
			'grades_created' => 0,
			'grades_updated' => 0,
			'skls_created' => 0,
			'skls_updated' => 0,
			'rows_processed' => 0,
		];

		$run = function () use (&$result, $rows, $columnMap, $subjectColumnMap, $majorMap, $schoolYearMap, $persist): void {
			foreach ($rows as $index => $row) {
				$excelRowNumber = $index + 1;

				if ($excelRowNumber < 4) {
					continue;
				}

				$schoolYearName = trim((string) $this->cell($row, $columnMap['TAHUN_PELAJARAN']));
				$nisn = trim((string) $this->cell($row, $columnMap['NISN']));
				$nis = trim((string) $this->cell($row, $columnMap['NIS']));
				$studentName = trim((string) $this->cell($row, $columnMap['NAMA_SISWA']));
				$pob = trim((string) $this->cell($row, $columnMap['TEMPAT_LAHIR']));
				$dobRaw = $this->cell($row, $columnMap['TGL_LAHIR']);
				$fatherName = trim((string) $this->cell($row, $columnMap['NAMA_AYAH']));
				$majorCode = trim((string) $this->cell($row, $columnMap['KODE_JURUSAN']));
				$letterNumber = trim((string) $this->cell($row, $columnMap['NO_SURAT']));
				$letterDateRaw = $this->cell($row, $columnMap['TGL_SURAT']);
				$publishedAtRaw = $this->cell($row, $columnMap['TGL_KELULUSAN']);

				if ($schoolYearName === '' && $nisn === '' && $nis === '' && $studentName === '') {
					continue;
				}

				if (
					$schoolYearName === '' ||
					$nisn === '' ||
					$nis === '' ||
					$studentName === '' ||
					$pob === '' ||
					$fatherName === '' ||
					$majorCode === '' ||
					$letterNumber === ''
				) {
					throw new RuntimeException("Data tidak boleh kosong pada baris {$excelRowNumber}. Kolom yang harus diisi: TAHUN_PELAJARAN, NISN, NIS, NAMA SISWA, TEMPAT_LAHIR, TGL_LAHIR, NAMA_AYAH, KODE_JURUSAN, NO_SURAT.");
				}

				$schoolYear = $schoolYearMap->get(strtoupper($schoolYearName));
				if (! $schoolYear) {
					throw new RuntimeException("Tahun pelajaran '{$schoolYearName}' pada baris {$excelRowNumber} tidak ditemukan di master School Year.");
				}

				$major = $majorMap->get(strtoupper($majorCode));
				if (! $major) {
					throw new RuntimeException("Kode jurusan '{$majorCode}' pada baris {$excelRowNumber} tidak ditemukan di master Major.");
				}

				$letterDate = $this->parseDate($letterDateRaw, $excelRowNumber, 'TGL_SURAT');
				$publishedAt = $this->parseDate($publishedAtRaw, $excelRowNumber, 'TGL_KELULUSAN')->startOfDay();
				$dob = $this->parseDate($dobRaw, $excelRowNumber, 'TGL_LAHIR')->startOfDay();

				$student = Student::query()
					->where('nis', $nis)
					->orWhere('nisn', $nisn)
					->first();
				$isExistingStudent = (bool) $student;

				$existingGradeSubjectIds = [];
				if ($isExistingStudent) {
					$existingGradeSubjectIds = Grade::query()
						->where('student_id', $student->id)
						->pluck('subject_id')
						->all();
				}

				if ($isExistingStudent) {
					$student->fill([
						'name' => $studentName,
						'pob' => $pob,
						'dob' => $dob->toDateString(),
						'nis' => $nis,
						'nisn' => $nisn,
						'father_name' => $fatherName,
						'major_id' => $major->id,
						'school_year_id' => $schoolYear->id,
					]);

					if ($student->isDirty()) {
						if ($persist) {
							$student->save();
						}
						$result['students_updated']++;
					}
				} else {
					if ($persist) {
						$student = Student::query()->create([
							'name' => $studentName,
							'pob' => $pob,
							'dob' => $dob->toDateString(),
							'nis' => $nis,
							'nisn' => $nisn,
							'father_name' => $fatherName,
							'major_id' => $major->id,
							'school_year_id' => $schoolYear->id,
						]);
					}

					$result['students_created']++;
				}

				$existingSkl = $isExistingStudent
					? Skl::query()->where('student_id', $student->id)->first()
					: null;

				if ($existingSkl) {
					if ($persist) {
						$existingSkl->fill([
							'letter_number' => $letterNumber,
							'status' => 'Lulus',
							'letter_date' => $letterDate->toDateString(),
							'published_at' => $publishedAt,
						])->save();
					}
					$result['skls_updated']++;
				} else {
					if ($persist) {
						Skl::query()->create([
							'student_id' => $student->id,
							'letter_number' => $letterNumber,
							'status' => 'Lulus',
							'letter_date' => $letterDate->toDateString(),
							'published_at' => $publishedAt,
							'is_questionnaire_completed' => false,
						]);
					}
					$result['skls_created']++;
				}

				foreach ($subjectColumnMap as $columnIndex => $subject) {
					$scoreRaw = $this->cell($row, $columnIndex);
					if ($scoreRaw === null || trim((string) $scoreRaw) === '') {
						continue;
					}

					if (! is_numeric((string) $scoreRaw)) {
						throw new RuntimeException("Nilai mapel '{$subject->kode}' pada baris {$excelRowNumber} harus berupa angka.");
					}

					$score = (int) round((float) $scoreRaw);
					if ($score < 0 || $score > 100) {
						throw new RuntimeException("Nilai mapel '{$subject->kode}' pada baris {$excelRowNumber} harus di rentang 0-100.");
					}

					if (! $isExistingStudent) {
						$result['grades_created']++;
						continue;
					}

					$isNewGrade = ! in_array($subject->id, $existingGradeSubjectIds, true);

					if ($persist) {
						$grade = Grade::query()->firstOrNew([
							'student_id' => $student->id,
							'subject_id' => $subject->id,
						]);

						$grade->score = $score;
						$grade->save();
					}

					if ($isNewGrade) {
						$result['grades_created']++;
					} else {
						$result['grades_updated']++;
					}
				}

				$result['rows_processed']++;
			}
		};

		if ($persist) {
			DB::transaction($run);
		} else {
			$run();
		}

		return $result;
	}

	/**
	 * @return array<int, array<int, mixed>>
	 */
	protected function readRows(string $filePath): array
	{
		$reader = new Reader();
		$rows = [];

		$reader->open($filePath);

		foreach ($reader->getSheetIterator() as $sheet) {
			foreach ($sheet->getRowIterator() as $row) {
				$rows[] = $row->toArray();
			}

			break;
		}

		$reader->close();

		return $rows;
	}

	/**
	 * @param  array<int, mixed>  $headerRow
	 * @return array<string, int>
	 */
	protected function resolveColumns(array $headerRow): array
	{
		$requiredHeaders = [
			'TAHUN_PELAJARAN',
			'NISN',
			'NIS',
			'NAMA_SISWA',
			'TEMPAT_LAHIR',
			'TGL_LAHIR',
			'NAMA_AYAH',
			'KODE_JURUSAN',
			'NO_SURAT',
			'TGL_SURAT',
			'TGL_KELULUSAN',
			'KODE_MATA_PELAJARAN',
		];

		$normalizedToIndex = [];
		foreach ($headerRow as $index => $value) {
			$normalized = $this->normalizeHeader((string) $value);
			if ($normalized !== '') {
				$normalizedToIndex[$normalized] = $index;
			}
		}

		$map = [];
		foreach ($requiredHeaders as $header) {
			if (! array_key_exists($header, $normalizedToIndex)) {
				throw new InvalidArgumentException("Header '{$header}' tidak ditemukan di baris 2 template.");
			}

			$map[$header] = $normalizedToIndex[$header];
		}

		$map['subject_start_column'] = $map['KODE_MATA_PELAJARAN'];

		return $map;
	}

	/**
	 * @param  array<int, mixed>  $subjectHeaderRow
	 * @param  int  $subjectStartColumn
	 * @return array<int, Subject>
	 */
	protected function resolveSubjectColumns(array $subjectHeaderRow, int $subjectStartColumn): array
	{
		$subjectsByCode = Subject::query()->get()->keyBy(fn (Subject $subject) => strtoupper(trim((string) $subject->kode)));

		$columnMap = [];
		foreach ($subjectHeaderRow as $index => $value) {
			if ($index <= $subjectStartColumn) {
				continue;
			}

			$subjectCode = strtoupper(trim((string) $value));
			if ($subjectCode === '') {
				continue;
			}

			$subject = $subjectsByCode->get($subjectCode);
			if (! $subject) {
				$excelColumn = $this->toExcelColumnName($index + 1);
				throw new RuntimeException("Kode mapel '{$subjectCode}' pada header kolom {$excelColumn} tidak ditemukan di master Subject.");
			}

			$columnMap[$index] = $subject;
		}

		if ($columnMap === []) {
			throw new InvalidArgumentException('Tidak ada kode mata pelajaran pada baris header mapel (baris 3).');
		}

		return $columnMap;
	}

	protected function parseDate(mixed $value, int $rowNumber, string $columnName): Carbon
	{
		if ($value instanceof DateTimeInterface) {
			return Carbon::parse($value->format('Y-m-d H:i:s'));
		}

		$stringValue = trim((string) $value);
		if ($stringValue === '') {
			throw new RuntimeException("Kolom {$columnName} pada baris {$rowNumber} wajib berisi tanggal.");
		}

		try {
			return Carbon::parse($stringValue);
		} catch (\Throwable) {
			throw new RuntimeException("Format tanggal {$columnName} pada baris {$rowNumber} tidak valid.");
		}
	}

	/**
	 * @param  array<int, mixed>  $row
	 */
	protected function cell(array $row, int $index): mixed
	{
		return $row[$index] ?? null;
	}

	protected function normalizeHeader(string $header): string
	{
		return strtoupper(str_replace(' ', '_', trim($header)));
	}

	protected function toExcelColumnName(int $columnNumber): string
	{
		$name = '';

		while ($columnNumber > 0) {
			$mod = ($columnNumber - 1) % 26;
			$name = chr(65 + $mod) . $name;
			$columnNumber = intdiv($columnNumber - 1, 26);
		}

		return $name;
	}
}
