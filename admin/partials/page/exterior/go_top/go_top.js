//平滑返回顶部
const goTop = () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
};
//超过一段距离，则隐藏
const topControl = document.getElementById("topcontrol");
window.addEventListener("scroll", () => {
  if (window.scrollY > 600) {
    topControl.classList.add("npcShow");
  } else {
    topControl.classList.remove("npcShow");
  }
});
