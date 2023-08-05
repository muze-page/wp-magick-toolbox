//存值
import { createContext } from "react";
//准备的默认值
const dataObject = {
  option: {
    name: "Npcink",
    age: 18,
    handle: true,
  },
};
const DataContext = createContext(dataObject);

export default DataContext;
