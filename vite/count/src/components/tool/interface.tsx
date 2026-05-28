export type Receive = {
  countData: DataLocal;
};

export type DataLocal = {
  single: {
    count: Array<Count>;
    today: ColumnMore;
    month: ColumnMore;
  };
};

export type Count = {
  title: string;
  num: number;
  unit: string;
  icon: string;
};

export type ColumnMore = {
  width: number;
  height: number;
  title: string;
  tooltip?: string;
  dataset: Array<Array<string | number>>;
};