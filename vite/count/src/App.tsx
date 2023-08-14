import "./App.css";
//打包前注释
//import "./load-styles.css";
import B2Shop from "@/components/page/b2Shop/index";
import SingleCount from "@/components/page/singleCount/index";

function App() {
  return (
    <>
      {/**
       * 销售统计
       */}

      <B2Shop />
      {/**
       * 周数据预览
       */}

      <SingleCount />
    </>
  );
}

export default App;
