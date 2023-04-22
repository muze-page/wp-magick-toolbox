console.info("测试下")
console.log(imageViewsData);
const App = Vue.createApp({
    setup() {
        const data = imageViewsData;

        const result = Object.values(data.reduce((res, { id, ad }) => {
            if (ad in res) {
                res[ad].count++;
            } else {
                res[ad] = { ad, count: 1 };
            }
            return res;
        }, {}));


        const newData = result.map(item => {
            if (item.ad === '2') {
                return { ...item, name: 'hh' };
            }

            if (item.ad === '2199') {
                return { ...item, name: '娜娜' };
            }

            return { ...item, name: '没有' };
        });


        return {

            newData,
        };
    },
});
App.mount("#Application");