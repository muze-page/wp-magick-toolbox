//准备初始数据
import { createContext } from "react";
import { DataLocal } from "./interface";
import option from "./defaultVar";

//开发环境状态
const state: boolean = import.meta.env.VITE_STATE;

//输出默认值
function getDataLocal(): DataLocal {
  if (state) {
    //开发
    return option;
  } else {
    console.log("收到的值")
    console.log((window as any).dataLocal?.countData);
    //打包
    return (window as any).dataLocal?.countData !== ""
      ? (window as any).dataLocal?.countData
      : {};
  }
}

//传值
const dataObject: DataLocal = getDataLocal();

const DataContext = createContext(dataObject);

export default DataContext;
