//各种请求
import axios from "axios";

//开发环境状态
const state: boolean = import.meta.env.VITE_STATE;
//输出ajaxurl
function getAjaxurl(): string {
  if (state) {
    //开发
    return import.meta.env.VITE_AJAXURL;
  } else {
    //打包
    return (window as any).ajaxurl;
  }
}
//传值
const ajaxurl = getAjaxurl();

//获取所有数据库表名字
export const get_all_table_name = async () => {
  const params = new URLSearchParams();
  params.append("action", "get_all_table_names");
  try {
    const response = await axios.post(ajaxurl, params);

    if (response.status === 200) {
      //保存成功
      console.log(response);
      return response.data.data;
    } else {
      console.error("出错：" + response.data);
    }
  } catch (error: any) {
    console.error("出错：" + error.message);
  }
};
