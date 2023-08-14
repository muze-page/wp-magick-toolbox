//发文统计 左表右块
import { useContext } from "react";
import ColumnMore from "@/components/block/column_more";
import { SinglePublishMonth } from "@/components/tool/defaultVar";
import DataContext from "@/components/tool/dataContext";

const App = () => {
  //拿到值
  const optionObj = useContext(DataContext) ?? { single: {} };

  //表格
  const DataPublish = optionObj.single?.month || SinglePublishMonth;

  return (
    <>
      <ColumnMore data={DataPublish} />
    </>
  );
};
export default App;
