//准备初始数据
import { createContext } from "react";
import Interface from "@/interface";

//开发环境状态
const state: boolean = import.meta.env.VITE_STATE;

//准备布尔值
const boo: boolean = import.meta.env.VITE_BOOLEAN;

//组建开发环境下的对象
const option = {
  option: {
    name: import.meta.env.VITE_OPTION_NAME,
    age: parseInt(import.meta.env.VITE_OPTION_AGE),
    handle: import.meta.env.VITE_OPTION_HANDLE === "true",
  },
  //优化
  optimize: {
    //站点
    site: {
      //禁止转义
      no_escape: boo,
      //关键词自动添加链接
      add_inks: boo,
    },
  },
};

//输出选项值
function getDataLocal(): Interface {
  if (state) {
    //开发
    return option;
  } else {
    //打包
    return (window as any).dataLocal.option;
  }
}

//传值
const dataObject: Interface = getDataLocal();

const DataContext = createContext(dataObject);

export default DataContext;
