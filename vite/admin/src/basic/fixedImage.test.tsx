import { createRef } from "react";
import { Form } from "antd";
import { cleanup, fireEvent, render, screen, within } from "@testing-library/react";
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";

import FixedImage from "@/basic/fixedImage";

const imageOptions = [
  { value: "default", label: "/default.png", title: "默认简洁" },
  { value: "red", label: "/red.png", title: "红色纯粹" },
];

const getComputedStyle = window.getComputedStyle.bind(window);

beforeEach(() => {
  vi.spyOn(window, "getComputedStyle").mockImplementation((element) =>
    getComputedStyle(element),
  );
});

afterEach(() => {
  cleanup();
  vi.restoreAllMocks();
});

describe("FixedImage", () => {
  it("保留 Form 标签、说明和错误状态，并显示当前样式", () => {
    const buttonRef = createRef<HTMLButtonElement>();
    const { container } = render(
      <Form initialValues={{ maintenance_tips: "default" }}>
        <Form.Item
          label="维护提示"
          name="maintenance_tips"
          extra="选择前端维护提示样式"
          validateStatus="error"
          help="请选择有效样式"
        >
          <FixedImage ref={buttonRef} alists={imageOptions} />
        </Form.Item>
      </Form>,
    );

    const changeButton = screen.getByRole("button", { name: /更换维护提示样式/ });
    const formLabel = container.querySelector('label[for="maintenance_tips"]');
    const extra = screen.getByText("选择前端维护提示样式");
    const error = screen.getByText("请选择有效样式");
    const errorDescription = error.closest<HTMLElement>("[id]");

    expect(changeButton).toHaveAttribute("id", "maintenance_tips");
    expect(buttonRef.current).toBe(changeButton);
    expect(formLabel).toBeInTheDocument();
    expect(changeButton).toHaveAttribute("aria-invalid", "true");
    expect(changeButton.getAttribute("aria-describedby")?.split(" ")).toEqual(
      expect.arrayContaining([extra.id, errorDescription?.id]),
    );
    expect(screen.getByText("当前样式：默认简洁")).toBeInTheDocument();
  });

  it("使用一个有名称的单选组，并只在确认时提交草稿值", () => {
    const onChange = vi.fn();
    render(
      <Form>
        <Form.Item>
          <FixedImage
            aria-label="更换维护提示样式"
            alists={imageOptions}
            value="default"
            onChange={onChange}
          />
        </Form.Item>
      </Form>,
    );

    fireEvent.click(screen.getByRole("button", { name: "更换维护提示样式" }));

    let dialog = screen.getByRole("dialog", { name: "选择您需要的样式" });
    let group = within(dialog).getByRole("radiogroup", { name: "维护提示样式" });
    expect(within(group).getByRole("radio", { name: "默认简洁" })).toBeChecked();
    expect(within(group).getAllByRole("radio")).toHaveLength(3);

    fireEvent.click(within(group).getByRole("radio", { name: "红色纯粹" }));
    fireEvent.click(within(dialog).getByRole("button", { name: "Cancel" }));
    expect(onChange).not.toHaveBeenCalled();

    fireEvent.click(screen.getByRole("button", { name: "更换维护提示样式" }));
    dialog = screen.getByRole("dialog", { name: "选择您需要的样式" });
    group = within(dialog).getByRole("radiogroup", { name: "维护提示样式" });
    expect(within(group).getByRole("radio", { name: "默认简洁" })).toBeChecked();

    fireEvent.click(within(group).getByRole("radio", { name: "红色纯粹" }));
    fireEvent.click(within(dialog).getByRole("button", { name: "OK" }));
    expect(onChange).toHaveBeenCalledOnce();
    expect(onChange).toHaveBeenCalledWith("red");
  });

  it("外部值变化后同步预览和下次打开时的选中项", () => {
    const { rerender } = render(
      <Form>
        <Form.Item>
          <FixedImage alists={imageOptions} value="default" onChange={vi.fn()} />
        </Form.Item>
      </Form>,
    );

    expect(screen.getByText("当前样式：默认简洁")).toBeInTheDocument();

    rerender(
      <Form>
        <Form.Item>
          <FixedImage alists={imageOptions} value="red" onChange={vi.fn()} />
        </Form.Item>
      </Form>,
    );
    expect(screen.getByText("当前样式：红色纯粹")).toBeInTheDocument();

    fireEvent.click(screen.getByRole("button", { name: /更换维护提示样式/ }));
    const dialog = screen.getByRole("dialog", { name: "选择您需要的样式" });
    const group = within(dialog).getByRole("radiogroup", { name: "维护提示样式" });
    expect(within(group).getByRole("radio", { name: "红色纯粹" })).toBeChecked();
  });

  it("允许由外层开关独立负责禁用状态", () => {
    render(
      <Form initialValues={{ maintenance_tips: "default" }}>
        <Form.Item name="maintenance_tips">
          <FixedImage alists={imageOptions} includeDisabled={false} />
        </Form.Item>
      </Form>,
    );

    fireEvent.click(screen.getByRole("button", { name: /更换维护提示样式/ }));
    const dialog = screen.getByRole("dialog", { name: "选择您需要的样式" });
    const group = within(dialog).getByRole("radiogroup", { name: "维护提示样式" });

    expect(within(group).getAllByRole("radio")).toHaveLength(2);
    expect(within(group).queryByRole("radio", { name: "禁用" })).not.toBeInTheDocument();
  });
});
