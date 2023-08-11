<script setup lang="ts">
import { ref } from "vue";
import Today from "../components/count/today.vue";
import Moon from "../components/count/moon.vue";
//选项
const activeTab = ref(0);
//列表
const tabs = ref([
  { title: "今日统计", component: Today },
  { title: "月度统计", component: Moon },
]);
//切换
const changeTab = (index: number) => {
  activeTab.value = index;
};
</script>
<template>
  <div class="tabs">
    <div
      v-for="(tab, index) in tabs"
      :key="index"
      :class="{ active: activeTab === index }"
      @click="changeTab(index)"
    >
      {{ tab.title }}
    </div>
  </div>

  <div class="tab-content">
    <component :is="tabs[activeTab].component" />
  </div>
</template>
<style scoped lang="less">
.tabs {
  display: flex;
  cursor: pointer;
  .active{
    padding: 6px 8px;
    border-radius: 6px;
    transition: all 0.3s;
    color: #fff;
    background-color: #3ac569;
    box-shadow: 5px 5px 10px #d9d9d9, -5px -5px 10px #ffffff;

  }
  > div {
    
    margin: 0 2em 0 0;
    padding: 6px 8px;
  }
}
</style>
