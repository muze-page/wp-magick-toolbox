import React from "react";
import { Calendar, CalendarProps, theme } from "antd";
import type { Dayjs } from "dayjs";
import { day_data } from "../tool/dataContext";

//月度
const getListData = (value: Dayjs) => {
  //拿到当前时间
  const time = value.format("YYYY-MM-DD");

  for (let i = 0; i < day_data.length; i++) {
    if (day_data[i].time === time) {
      return day_data[i];
    }
  }

  return null;
};

//对比时间
const compareDates = (date1: string, date2: string) => {
  // 将字符串形式的时间转换为日期对象
  const d1 = new Date(date1);
  const d2 = new Date(date2);

  // 使用时间戳进行比较
  if (d1.getTime() === d2.getTime()) {
    return 0; // 时间相等
  } else if (d1.getTime() < d2.getTime()) {
    return -1; // 第一个时间小于第二个时间
  } else {
    return 1; // 第一个时间大于第二个时间
  }
};

//拿到今天的时间
const getCurrentDate = () => {
  const today = new Date();

  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, "0"); // 月份从 0 开始，需要加 1
  const day = String(today.getDate()).padStart(2, "0");

  return `${year}-${month}-${day}`;
};

const dateCellRender = (value: Dayjs) => {
  const listData = getListData(value);

  const styles = { "--bgColor": listData?.color } as React.CSSProperties;

  //当前时间大于表格时间为true
  const today = getCurrentDate();
  const tableTime = value.format("YYYY-MM-DD");
  const switchTime = compareDates(today, tableTime) === 1;
  return (
    <div className="calendar-box" style={styles}>
      <span>{switchTime ? listData?.total ?? "0" : ""}</span>
    </div>
  );
};

//年度
const getMonthData = (value: Dayjs) => {
  if (value.month() === 8) {
    return 1394;
  }
};

const App: React.FC = () => {
  const monthCellRender = (value: Dayjs) => {
    const num = getMonthData(value);
    return num ? (
      <div className="notes-month">
        <section>{num}</section>
        <span>Backlog number</span>
      </div>
    ) : null;
  };

  const cellRender: CalendarProps<Dayjs>["cellRender"] = (current, info) => {
    if (info.type === "date") return dateCellRender(current);
    if (info.type === "month") return monthCellRender(current);
    return info.originNode;
  };

  //卡片
  const { token } = theme.useToken();
  const wrapperStyle: React.CSSProperties = {
    width: 900,
    border: `1px solid ${token.colorBorderSecondary}`,
    borderRadius: token.borderRadiusLG,
  };

  return (
    <>
      <h2>年度销售额</h2>
      <div style={wrapperStyle}>
        <Calendar
          cellRender={cellRender}
          fullscreen={false}
          style={{ overflow: "hidden" }}
        />
      </div>
    </>
  );
};

export default App;
