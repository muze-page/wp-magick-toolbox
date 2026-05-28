import Oss from "@/components/performance/oss";
import SeoChecker from "@/components/performance/seo_checker";
import MediaHealth from "@/components/performance/media_health";
import SearchEnhance from "@/components/performance/search_enhance";
import DbClean from "@/components/performance/db_clean";

const App: React.FC = () => {
  return (
    <>
      <Oss />
      <SeoChecker />
      <MediaHealth />
      <SearchEnhance />
      <DbClean />
    </>
  );
};

export default App;
