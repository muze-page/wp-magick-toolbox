//销售表格
import { useContext } from "react";
import Column from "@/components/block/column";
import DataContext from "@/components/tool/dataContext";
import { ShopForm } from "@/components/tool/defaultVar";
const App = () => {
  //拿到值
  const optionObj = useContext(DataContext) ?? { shop: {} };

  //表格
  const DataPublish = optionObj.shop?.form || ShopForm;
  return (
    <>
      <div className="form-box">
        {DataPublish.map((item, index) => (
          <Column key={index} data={item} />
        ))}
      </div>
    </>
  );
};
export default App;
