import "./App.css"
import {  Col, Row } from "antd";
import Tab from "./components/tab";
import Save from "./tool/save";
import React from "react";

const App: React.FC = () => {

  return (
   
    <div className="mami_option">
      <Row>
        <Col span={16}>
          <Tab />
        </Col>
        <Col span={8}>
          
            <Save  />
          
        </Col>
      </Row>
      </div>
    
  );
};

export default App;
