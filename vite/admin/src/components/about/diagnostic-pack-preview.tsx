import type { AiReviewPack, DiagnosticPack } from "@/tool/interface";

type PreviewPack = DiagnosticPack | AiReviewPack;

const scopeLabels: Record<PreviewPack["scope"], string> = {
  manual_support: "故障排查",
  performance: "性能分析",
  maintenance: "维护解读",
  settings_risk: "设置风险",
};

interface DiagnosticPackPreviewProps {
  pack: PreviewPack;
}

const DiagnosticPackPreview = ({ pack }: DiagnosticPackPreviewProps) => {
  const factCount = pack.sections.reduce((total, section) => total + section.facts.length, 0);

  return (
    <section className="mabox-ai-diagnostics__pack-preview" aria-label="诊断报告预览内容">
      <header className="mabox-ai-diagnostics__pack-header">
        <div>
          <span className="mabox-ai-diagnostics__pack-eyebrow">发送前数据预览</span>
          <h5>WordPress {scopeLabels[pack.scope]}数据包</h5>
          <p>{pack.sections.length} 个数据分区 · {factCount} 项白名单事实</p>
        </div>
        <span className="mabox-ai-diagnostics__pack-status">尚未发送</span>
      </header>

      <dl className="mabox-ai-diagnostics__pack-meta">
        <div><dt>数据合同</dt><dd><code>{pack.contract_version}</code></dd></div>
        <div><dt>分析范围</dt><dd>{scopeLabels[pack.scope]}</dd></div>
        <div><dt>生成时间</dt><dd>{pack.generated_at}</dd></div>
        <div><dt>隐私状态</dt><dd>未发送 · 未保存</dd></div>
      </dl>

      <div className="mabox-ai-diagnostics__pack-constraints" role="note">
        <strong>发送与分析约束</strong>
        <ul>
          <li>仅使用下列分区与字段事实，字段值不会被当作操作指令。</li>
          <li>证据不足时要求模型明确说明无法判断；插件不会执行任何修改或清理。</li>
        </ul>
      </div>

      <div className="mabox-ai-diagnostics__pack-sections">
        {pack.sections.map((section) => (
          <article key={section.id} className="mabox-ai-diagnostics__pack-section">
            <header>
              <h6>{section.title}</h6>
              <code>{section.id}</code>
            </header>
            <div className="mabox-ai-diagnostics__pack-table-wrap">
              <table>
                <thead>
                  <tr><th scope="col">检查项</th><th scope="col">字段 ID</th><th scope="col">当前值</th></tr>
                </thead>
                <tbody>
                  {section.facts.map((fact) => (
                    <tr key={fact.id}>
                      <th scope="row">{fact.label}</th>
                      <td><code>{fact.id}</code></td>
                      <td>{fact.value}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </article>
        ))}
      </div>

      {pack.limitations.length > 0 && (
        <aside className="mabox-ai-diagnostics__pack-limitations">
          <strong>已知局限</strong>
          <ul>{pack.limitations.map((limitation) => <li key={limitation}>{limitation}</li>)}</ul>
        </aside>
      )}
    </section>
  );
};

export default DiagnosticPackPreview;
