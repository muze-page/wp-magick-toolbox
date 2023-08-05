//准备对象类型
//准备类型
type DataLocal = {
  option: FieldType;
  optimize: Optimize;
};

type FieldType = {
  name?: string;
  age?: number;
  handle?: boolean;
};

//优化
type Optimize = {
  //站点
  site: {
    //禁止转义
    no_escape: boolean;
    //关键词自动添加链接
    add_inks: boolean;
  };
};

export default DataLocal;
