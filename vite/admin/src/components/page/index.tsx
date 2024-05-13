//聚合
import Comment from "@/components/page/comment";
import Feature from "@/components/page/feature";
import Function from "@/components/page/function";
const App: React.FC = () => {
  return (
    <>
      <Feature /> {/**外观 */}
      <Comment /> {/**评论 */}
      <Function />
      {/**功能 */}
    </>
  );
};

export default App;
