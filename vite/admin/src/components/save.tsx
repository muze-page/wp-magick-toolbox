//保存按钮
//将拿到的值推送到服务器端
import { useContext } from "react";
import axios from "axios";
import { Button } from "antd";
import DataContext from "../dataContext";

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

const App: React.FC = () => {
  //拿到值
  const optionObj = useContext(DataContext);

  //提交动作
  const postData = async () => {
    const params = new URLSearchParams();
    params.append("action", "save_object_option");
    params.append("object_data", JSON.stringify(optionObj));
    try {
      const response = await axios.post(ajaxurl, params);

      if (response.status === 200) {
        console.log("设置选项已保存！");
        console.log(response);
        alert("保存成功，现在可以使用查询功能了");
      } else {
        console.error("保存设置选项时出错：" + response.data);
      }
    } catch (error: any) {
      console.error("保存设置选项时出错：" + error.message);
    }
  };
  return (
    <>
      <Button type="primary" onClick={postData}>
        保存
      </Button>
    </>
  );
};

export default App;
