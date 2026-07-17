import React from "react";
import { Input } from "antd";

type TextAreaHtmlProps = Omit<
  React.ComponentProps<typeof Input.TextArea>,
  "defaultValue" | "onChange" | "value"
> & {
  value?: string;
  onChange?: (value: string) => void;
};

const decodeHtmlTags = (value = "") => {
  const textArea = document.createElement("textarea");
  textArea.innerHTML = value;

  return textArea.value;
};

const encodeHtmlTags = (value: string) =>
  value.replace(/</g, "&lt;").replace(/>/g, "&gt;");

const TextAreaHtml = React.forwardRef<
  React.ElementRef<typeof Input.TextArea>,
  TextAreaHtmlProps
>(({ value = "", onChange, rows = 4, ...textAreaProps }, ref) => (
  <Input.TextArea
    {...textAreaProps}
    ref={ref}
    rows={rows}
    value={decodeHtmlTags(value)}
    onChange={(event) => onChange?.(encodeHtmlTags(event.target.value))}
  />
));

TextAreaHtml.displayName = "TextAreaHtml";

export default TextAreaHtml;
