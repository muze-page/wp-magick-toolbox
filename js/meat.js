//打印下，拿到的值
console.info("拿到的值是")
console.log(arr)
//拿到待渲染的节点
let showDiv = document.getElementById("show");

// 使用 map() 方法将数组转换为字符串，并以换行符分隔元素
let str = arr.map(item => "我的名字是：" + item + "<br>").join("");

// 将字符串插入到 HTML 页面中的指定元素内
showDiv.innerHTML = str;