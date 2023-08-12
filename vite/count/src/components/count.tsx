//销售表格
import Column from "./block/column";
import { FormShop } from "./tool/defaultVar";
const App = () => {
  return (
    <>
      <div className="form-box">
        {FormShop.map((item, index) => (
          <Column key={index} data={item} />
        ))}
      </div>
    </>
  );
};
export default App;
