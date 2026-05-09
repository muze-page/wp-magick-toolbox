import React, { useState } from "react";
import { Card, Row, Col, Tag } from "antd";
import { StarFilled, MenuOutlined } from "@ant-design/icons";
import { DndContext, closestCenter, KeyboardSensor, PointerSensor, useSensor, useSensors, DragEndEvent } from "@dnd-kit/core";
import { arrayMove, SortableContext, sortableKeyboardCoordinates, useSortable, horizontalListSortingStrategy } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import { getFavorites, reorderFavorites } from "@/tool/favorites";
import { searchIndex } from "@/components/feature-search";

interface FavoritesPanelProps {
  optionData: any;
}

function SortableItem({ item }: { item: { id: string; label: string; tabKey: string; enabled: boolean } }) {
  const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable({ id: item.id });

  const style: React.CSSProperties = {
    transform: CSS.Transform.toString(transform),
    transition,
    opacity: isDragging ? 0.5 : 1,
    cursor: "grab",
  };

  return (
    <Col xs={12} sm={8} md={6} lg={4} ref={setNodeRef} style={style}>
      <Card
        size="small"
        hoverable
        onClick={() => {
          const el = document.getElementById(item.id);
          if (el) {
            el.scrollIntoView({ behavior: "smooth", block: "center" });
            el.style.transition = "background 0.3s";
            el.style.background = "#e6f4ff";
            setTimeout(() => { el.style.background = ""; }, 2000);
          }
        }}
        bodyStyle={{ padding: "12px 16px", textAlign: "center" }}
      >
        <div style={{ display: "flex", alignItems: "center", justifyContent: "center", gap: 4, marginBottom: 4 }}>
          <span {...attributes} {...listeners} style={{ cursor: "grab", color: "#999", fontSize: 14 }}>
            <MenuOutlined />
          </span>
          <span style={{ fontSize: 14, fontWeight: 500 }}>{item.label}</span>
        </div>
        <Tag color={item.enabled ? "success" : "default"}>
          {item.enabled ? "已启用" : "已关闭"}
        </Tag>
      </Card>
    </Col>
  );
}

const FavoritesPanel: React.FC<FavoritesPanelProps> = ({ optionData }) => {
  const [items, setItems] = useState(() => {
    const favIds = getFavorites();
    return favIds.map((id: string) => {
      const item = searchIndex.find((s: any) => s.id === id);
      if (!item) return null;
      const parts = id.split("-");
      let enabled = false;
      try {
        let current = optionData;
        for (let i = 0; i < parts.length; i++) {
          current = current[parts[i]];
        }
        enabled = typeof current === "boolean" ? current : current !== "false" && current !== "";
      } catch (e) {
        enabled = false;
      }
      return { id, label: item.label, tabKey: item.tabKey, enabled };
    }).filter(Boolean);
  });

  const sensors = useSensors(
    useSensor(PointerSensor, { activationConstraint: { distance: 8 } }),
    useSensor(KeyboardSensor, { coordinateGetter: sortableKeyboardCoordinates })
  );

  const handleDragEnd = (event: DragEndEvent) => {
    const { active, over } = event;
    if (over && active.id !== over.id) {
      setItems((prev) => {
        const oldIndex = prev.findIndex((item) => item?.id === active.id);
        const newIndex = prev.findIndex((item) => item?.id === over.id);
        const newOrder = arrayMove(prev, oldIndex, newIndex);
        reorderFavorites(newOrder.map((item) => item!.id));
        return newOrder;
      });
    }
  };

  if (items.length === 0) return null;

  return (
    <Card title={<span><StarFilled style={{ color: "#faad14", marginRight: 8 }} />我的常用工具（拖拽排序）</span>}>
      <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
        <SortableContext items={items.map((i) => i!.id)} strategy={horizontalListSortingStrategy}>
          <Row gutter={[16, 16]}>
            {items.map((item) => item && <SortableItem key={item.id} item={item} />)}
          </Row>
        </SortableContext>
      </DndContext>
    </Card>
  );
};

export default FavoritesPanel;
