//保存按钮
//将拿到的值推送到服务器端
import { useContext } from "react";
import axios from "axios";
import { Button, message, Affix } from "antd";
import DataContext from "./dataContext";

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
  //提示信息
  const [messageApi, contextHolder] = message.useMessage();
  const success = () => {
    messageApi.open({
      type: "success",
      content: "保存成功",
      style: {
        marginTop: "10vh",
      },
    });
  };
  const warning = () => {
    messageApi.open({
      type: "warning",
      content: "保存失败",
      style: {
        marginTop: "10vh",
      },
    });
  };

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
        //保存成功
        console.log(response);

        success();
      } else {
        console.error("保存设置选项时出错：" + response.data);

        warning();
      }
    } catch (error: any) {
      console.error("保存设置选项时出错：" + error.message);
    }
  };
  return (
    <>
      {contextHolder}
      <Affix offsetTop={120}>
        <Button type="primary" onClick={postData}>
          保存
        </Button>
      </Affix>
    </>
  );
};

export default App;
