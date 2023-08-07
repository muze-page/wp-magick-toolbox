//准备对象类型

//准备类型
export type DataLocal = {
  option: FieldType;
  optimize: {
    site: OptimizeSite;
    medium: OptimizeMedium;
    comment: OptimizeComment;
    other: OptimizeOther;
  };
};

type FieldType = {
  name?: string;
  age?: number;
  handle?: boolean;
};

//优化 站点
export type OptimizeSite = {
  //禁止转义
  no_escape: boolean;
  //关键词自动添加链接
  add_inks: boolean;
};

//优化 媒体
export type OptimizeMedium = {
  img_add_tag: boolean;
  no_auto_size: boolean;
  medium_add_svg: boolean;
  upload_auto_name: string;
};

//优化 评论
export type OptimizeComment = {
  interval: boolean; //两次评论间隔
  interval_time: number; //间隔时间
  words_number: boolean; //是否开启字数控制
  words_number_min: number; //最少评论字数
  words_number_max: number; //最多评论字数
  english: boolean; //禁止纯英文评论
  japanese: boolean; //禁止纯日文评论
  only: boolean; //单篇文章仅限评论一次
};

//优化 其他
export type OptimizeOther = {
  //筛选
  add_user: boolean; //作者筛选
  add_time: boolean; //时间筛选
  //显示ID
  show_id: boolean; //列表显示ID
};
