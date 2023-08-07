//默认变量
//准备布尔值
const boo: boolean = import.meta.env.VITE_BOOLEAN === true;

//准备字符串false
const fal: string = import.meta.env.VITE_BOOLEAN;

//准备数字
const num: number = import.meta.env.VITE_BOOLEAN ? 1 : 0;

const App = {
  option: {
    name: import.meta.env.VITE_OPTION_NAME,
    age: parseInt(import.meta.env.VITE_OPTION_AGE),
    handle: import.meta.env.VITE_OPTION_HANDLE === "true",
  },
  //优化
  optimize: {
    //站点
    site: {
      //禁止转义
      no_escape: boo,
      //关键词自动添加链接
      add_inks: boo,
    },
    medium: {
      img_add_tag: boo,
      no_auto_size: boo,
      medium_add_svg: boo,
      upload_auto_name: fal,
    },
    comment: {
      interval: boo, //两次评论间隔
      interval_time: num, //两次评论间隔
      words_number: boo, //是否开启字数控制
      words_number_min: num, //最少评论字数
      words_number_max: num, //最多评论字数
      english: boo, //禁止纯英文评论
      japanese: boo, //禁止纯日文评论
      only: boo, //单篇文章仅限评论一次
    },
    other: {
      //筛选
      add_user: boo, //作者筛选
      add_time: boo, //时间筛选
      //显示ID
      show_id: boo, //列表显示ID
    },
  },
};

export default App;
