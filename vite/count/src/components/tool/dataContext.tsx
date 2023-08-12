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
    //打包
    return (window as any).dataLocal.option !== ""
      ? (window as any).dataLocal.option
      : {};
  }
}

//传值
const dataObject: DataLocal = getDataLocal();

const DataContext = createContext(dataObject);

export default DataContext;
