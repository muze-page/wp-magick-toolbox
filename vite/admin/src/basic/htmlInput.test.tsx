import { Form } from "antd";
import { cleanup, fireEvent, render, screen } from "@testing-library/react";
import { afterEach, describe, expect, it, vi } from "vitest";

import TextAreaHtml from "@/basic/htmlInput";

afterEach(cleanup);

describe("TextAreaHtml", () => {
  it("保留 Form 注入的标签和说明关联", () => {
    render(
      <Form initialValues={{ content: "&lt;p&gt;提示&lt;/p&gt;" }}>
        <Form.Item label="提示内容" name="content" extra="支持 HTML">
          <TextAreaHtml />
        </Form.Item>
      </Form>,
    );

    const textArea = screen.getByRole("textbox", { name: "提示内容" });
    const description = screen.getByText("支持 HTML");

    expect(textArea).toHaveValue("<p>提示</p>");
    expect(textArea).toHaveAttribute("id");
    expect(textArea).toHaveAttribute("aria-describedby", description.id);
  });

  it("外部值变化时同步显示，并继续输出转义后的值", () => {
    const onChange = vi.fn();
    const { rerender } = render(
      <TextAreaHtml aria-label="HTML 内容" value="&lt;p&gt;旧值&lt;/p&gt;" onChange={onChange} />,
    );

    const textArea = screen.getByRole("textbox", { name: "HTML 内容" });
    expect(textArea).toHaveValue("<p>旧值</p>");

    rerender(
      <TextAreaHtml aria-label="HTML 内容" value="&lt;p&gt;新值&lt;/p&gt;" onChange={onChange} />,
    );
    expect(textArea).toHaveValue("<p>新值</p>");

    fireEvent.change(textArea, { target: { value: "<strong>保存</strong>" } });
    expect(onChange).toHaveBeenCalledWith("&lt;strong&gt;保存&lt;/strong&gt;");
  });
});
