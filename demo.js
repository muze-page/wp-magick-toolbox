const App = Vue.createApp({
    setup() {


        //原始数据数组
        const dataList = Vue.reactive([
            { id: '2', date: '2023-04-20', count: '1' },
            { id: '3', date: '2023-04-20', count: '1' },
            { id: '4', date: '2023-04-20', count: '1' },
            { id: '2', date: '2023-04-20', count: '1' }
        ]);
        //替换数组
        const replaceId = [
            { id: '2', name: "五一投放图片展现广告" },
            { id: '2199', name: "五一投放图片点击广告" }
        ];

        //选择的结果值
        const selectedId = Vue.ref('All');
        //展示的筛选值
        const filteredData = Vue.computed(() => {

            if (selectedId.value === 'All') {
                const data = replace(dataList, replaceId);
                return data;
            } else {
                const arr = dataList.filter(item => item.id === selectedId.value);
                const data = replace(arr, replaceId);
                return data;
            }
        });

        //拿到ID组成选项数组
        const arrIdFn = () => {
            // 用 map 方法获取 ID 数组
            const idList = dataList.map(item => item.id);

            // 用 Set 数据结构进行去重
            const uniqueIdList = [...new Set(idList)];

            // 按从小到大的顺序排序
            uniqueIdList.sort((a, b) => a - b);
            //插入all
            uniqueIdList.unshift("All");
            return uniqueIdList;
        }
        const arrId = Vue.ref();
        arrId.value = arrIdFn();


        //添加ID对应计划关系
        const replace = (data, replaceId) => {
            const a = data;
            const b = replaceId;
            const bMap = new Map(b.map(item => [item.id, item.name]));
            console.info(bMap)
            a.forEach(item => {
                item.name = bMap.get(item.id) || '没有计划';
            });
            return a;
        }


        return {
            selectedId,
            filteredData,
            arrId,

        };

    },
    //template: ``
});
App.mount("#Application");