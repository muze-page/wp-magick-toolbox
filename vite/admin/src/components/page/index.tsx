//聚合
import Comment from "@/components/page/comment";
import Feature from "@/components/page/feature";
const App: React.FC = () => {
  return (
    <>
      <Feature /> {/**外观 */}
      <Comment /> {/**评论 */}
    </>
  );
};

export default App;
