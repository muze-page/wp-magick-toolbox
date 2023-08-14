//发文统计 左表右块
import { useContext } from "react";
import Count from "@/components/block/count";
import { SingleCount } from "@/components/tool/defaultVar";
import DataContext from "@/components/tool/dataContext";

const App = () => {
  //拿到值
  const optionObj = useContext(DataContext) ?? { single: {} };

  //列表
  const DataCount = optionObj.single?.count || SingleCount;
  return (
    <>
      {DataCount.map((item, index) => (
        <Count key={index} data={item} />
      ))}
    </>
  );
};
export default App;
