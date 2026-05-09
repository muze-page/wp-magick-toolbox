//准备初始数据
import { createContext } from "react";
import { Receive, DataLocal, DayData } from "./interface";
import option from "./defaultVar";

//开发环境状态
const state: boolean = import.meta.env.VITE_STATE;

//输出默认值
function getDataLocal(): Receive {
  if (state) {
    //开发
    return option;
  } else {
    //console.log("收到的值")
    //console.log((window as any).dataLocal?.countData);
    //打包
    return (window as any).dataLocal !== "" ? (window as any).dataLocal : { countData: { shop: { today: [], month: [], form: [] }, single: { count: [], today: { width: 0, height: 0, title: "", dataset: [] }, month: { width: 0, height: 0, title: "", dataset: [] } } }, day_data: [] };
  }
}

//传值
const dataObject: DataLocal = getDataLocal()?.countData;

const DataContext = createContext(dataObject);

//每天的数据
export const day_data: DayData[] = getDataLocal()?.day_data;

export default DataContext;
