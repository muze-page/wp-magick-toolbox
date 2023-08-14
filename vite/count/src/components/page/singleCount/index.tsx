//作者发文统计页面
import { useContext } from "react";
import DataContext from "@/components/tool/dataContext";
import Count from "@/components/page/singleCount/count_single";
import Moon from "@/components/page/singleCount/moon";
function App() {
  //拿到值
  const optionObj = useContext(DataContext);
  const state = optionObj.single ? true : false;

  return (
    <>
      {/**
       * 若传值则展示，
       */}
      {state && (
        <>
          <h3>文章统计</h3>
          <hr />
          
          <Count />
          <Moon />
        </>
      )}
    </>
  );
}

export default App;
