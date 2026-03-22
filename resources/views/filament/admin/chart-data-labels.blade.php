<script>
    window.filamentChartJsGlobalPlugins = window.filamentChartJsGlobalPlugins ?? [];

    if (! window.filamentChartJsGlobalPlugins.some((plugin) => plugin.id === 'persistentDataLabels')) {
        window.filamentChartJsGlobalPlugins.push({
            id: 'persistentDataLabels',
            afterDatasetsDraw(chart, args, pluginOptions) {
                const options = chart?.options?.plugins?.persistentDataLabels ?? pluginOptions ?? {};

                if (! options.enabled) {
                    return;
                }

                const { ctx } = chart;

                ctx.save();
                ctx.font = options.font ?? '600 12px Inter, sans-serif';
                ctx.fillStyle = options.color ?? '#111827';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                chart.data.datasets.forEach((dataset, datasetIndex) => {
                    const meta = chart.getDatasetMeta(datasetIndex);

                    if (! meta || meta.hidden) {
                        return;
                    }

                    meta.data.forEach((element, index) => {
                        const value = Number(dataset.data?.[index] ?? 0);

                        if (! Number.isFinite(value) || value === 0) {
                            return;
                        }

                        const position = element.tooltipPosition();
                        const chartType = chart.config.type;
                        let x = position.x;
                        let y = position.y;

                        if (chartType === 'bar') {
                            y -= 14;
                        }

                        ctx.fillText(String(value), x, y);
                    });
                });

                ctx.restore();
            },
        });
    }
</script>