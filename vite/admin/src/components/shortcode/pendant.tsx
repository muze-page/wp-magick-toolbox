import { useState, useContext, useEffect } from "react";
import { DataContext } from "@/tool/dataContext";
import { CodePendant } from "@/tool/interface";
import { defaultVarOption } from "@/tool/defaultVar";
import { ModuleCard } from "@/components/settings-ui";
import Map from "@/basic/mapTable";
import Zuji from "@/assets/shortcode/pendant/足迹.png";

type FieldType = CodePendant;

const App: React.FC = () => {
  const { optionData, updateOption } = useContext(DataContext);
  const publicData =
    optionData.shortcode?.pendant || defaultVarOption.shortcode.pendant;

  const [formData, setFormData] = useState(publicData || {});

  const onValuesChange = (
    changedValues: Partial<FieldType>,
    _allValues: FieldType
  ) => {
    setFormData((prevState) => ({
      ...prevState,
      ...changedValues,
    }));
  };

  useEffect(() => {
    updateOption("shortcode", "pendant", formData);
  }, [formData]);

  return (
    <div className="mabox-module-grid">
      <ModuleCard
        title="足迹地图"
        description="在简单的中国地图上展示你的足迹"
        featureId="shortcode-pendant-merc_map"
        enabled={formData.merc_map as boolean}
        onChange={(checked: boolean) => {
          onValuesChange({ merc_map: checked } as Partial<FieldType>, formData);
        }}
        preview={{ title: "足迹", img: Zuji }}
      >
        <div>
          <div style={{ fontSize: 13, color: "#666", marginBottom: 4 }}>地点</div>
          <Map />
          <div style={{ fontSize: 12, color: "#999", marginTop: 4 }}>
            需填写地址和经纬度，保留两位小数；
            <a href="https://jingweidu.bmcx.com/" target="_blank" rel="noreferrer">
              经纬度查询
            </a>
          </div>
        </div>
      </ModuleCard>
    </div>
  );
};

export default App;
