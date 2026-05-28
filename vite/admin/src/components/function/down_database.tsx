import React from "react";
import { useState, useEffect } from "react";
import { Form, Select, Button } from "antd";
import { DownloadOutlined } from "@ant-design/icons";
import { AntConfig } from "@/tool/tool";
import { ListData } from "@/tool/interface";
import { get_all_table_name, get_table_data } from "@/axios/axios";
import { SettingsSection } from "@/components/settings-ui";

const fromConfig = AntConfig.from;

const App: React.FC = () => {
  const [table_list, set_table_list] = useState<ListData[]>([]);

  const [selected, setSelected] = useState<string>("");

  const onChange = (value: string) => {
    setSelected(value);
  };

  const filterOption = (
    input: string,
    option?: { label: string; value: string }
  ) => (option?.label ?? "").toLowerCase().includes(input.toLowerCase());

  const get_table = async () => {
    try {
      const list = await get_all_table_name();

      const newArray = list.map((item: any) => ({
        label: item,
        value: item,
      }));
      set_table_list(newArray);
    } catch (error) {
      console.error("Error fetching table data:", error);
    }
  };

  const get_data = async () => {
    await get_table_data(selected);
  };

  useEffect(() => {
    get_table();
  }, []);

  return (
    <SettingsSection title="数据库导出">
      <Form
        name="down_database"
        labelCol={fromConfig.labelCol}
        wrapperCol={fromConfig.wrapperCol}
        style={{ maxWidth: fromConfig.maxWidth }}
        autoComplete="off"
        onFinish={() => {}}
      >
        <Form.Item label="选择数据库" extra={"选中您需要下载的数据库"}>
          <Select
            showSearch
            optionFilterProp="children"
            style={{ width: 200 }}
            onChange={onChange}
            filterOption={filterOption}
            options={table_list}
          />
        </Form.Item>
        <Form.Item label="点击">
          <Button
            type="primary"
            icon={<DownloadOutlined />}
            onClick={() => get_data()}
          >
            下载 {selected}
          </Button>
        </Form.Item>
      </Form>
    </SettingsSection>
  );
};

export default App;
