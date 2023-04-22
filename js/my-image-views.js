console.info("测试下")
console.log(imageViewsData);
const App = Vue.createApp({
    setup() {
        const data = imageViewsData;

        const newData = data.map(item => {
            if (item.id === '2') {
                return { ...item, name: 'hh' };
            }

            if (item.id === '2199') {
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