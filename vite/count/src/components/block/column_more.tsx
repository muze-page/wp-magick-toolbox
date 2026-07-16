//多人版
import { useEffect, useMemo, useRef } from "react";
import * as echarts from "echarts/core";
import {
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
} from "echarts/components";
import { BarChart } from "echarts/charts";
import { CanvasRenderer } from "echarts/renderers";
import { ColumnMore } from "@/components/tool/interface";
echarts.use([
  DatasetComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
  BarChart,
  CanvasRenderer,
]);

const App = ({ data }: { data: ColumnMore }) => {
  const option = useMemo(() => {
    //获取type数量
    const num = data.dataset[0].length - 1;
    const series = Array.from({ length: num }, () => ({ type: "bar" }));

    return {
      title: {
        text: data.title,
      },
      tooltip: {
        valueFormatter: (value: number) => value.toFixed(0) + (data.tooltip ?? "篇"),
      },
      legend: {},
      dataset: {
        source: data.dataset,
      },
      xAxis: { type: "category" },
      yAxis: {},
      //声明几个条形系列，每个都将被映射
      //默认情况下为dataset.source的列。
      series,
    };
  }, [data]);

  //准备节点
  const chartRefs = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const chartElement = chartRefs.current;
    if (!chartElement) return;

    //找节点
    const myChart = echarts.init(chartElement);

    //做数据
    myChart.setOption(option);

    const resizeChart = () => myChart.resize();
    const resizeObserver = typeof ResizeObserver === "undefined"
      ? null
      : new ResizeObserver(resizeChart);
    resizeObserver?.observe(chartElement);
    window.addEventListener("resize", resizeChart);

    // 清除图表实例
    return () => {
      resizeObserver?.disconnect();
      window.removeEventListener("resize", resizeChart);
      myChart.dispose();
    };
  }, [option]);

  return (
    <div
      className="count-chart"
      ref={chartRefs}
      style={{
        width: "100%",
        maxWidth: `${data.width ?? 600}px`,
        height: `${data.height ?? 300}px`,
      }}
    ></div>
  );
};

export default App;
