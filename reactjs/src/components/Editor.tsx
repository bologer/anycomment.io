import React, {RefObject, useEffect, useMemo} from 'react';
import ReactQuill from 'react-quill';
import styled from 'styled-components';

const QuillWrapper = styled.div`
  width: 100%;
  background-color: #fff;
  border-radius: 5px;
`;

const ToolbarWrapper = styled.div`
  border-top: 1px solid #c9cbcc;
  padding: 5px;
`;

let toolbarId: string = 'toolbar';

const ref: RefObject<any> = React.createRef();

/**
 * Renders toolbar.
 *
 * @param toolbarId
 * @return {*}
 * @constructor
 */
function Toolbar({toolbarId}: { toolbarId: string }) {

    return (
        <ToolbarWrapper id={toolbarId} className="ql-toolbar ql-snow">
            <button type="button" className="ql-bold"/>
            <button type="button" className="ql-italic"/>
            <button type="button" className="ql-list" value="ordered"/>
            <button type="button" className="ql-list" value="bullet"/>
            <button type="button" className="ql-blockquote"/>
        </ToolbarWrapper>
    );
}


export interface EditorProps {
    onChange: (e: any) => void;
    value: string | number;
    className?: string;
    theme?: string;
    placeholder?: string | null;
    modules?: {};
    formats?: string[];
    showToolbar?: boolean;
    shouldFocus?: boolean;
    emptyContent?: boolean;
    entropy?: string | number;
    refHandler?: (editorRef: any) => void;
}

/**
 * Custom editor component wrapper around React Quill.
 * @param onChange
 * @param className
 * @param theme
 * @param value
 * @param placeholder
 * @param modules
 * @param formats
 * @param showToolbar
 * @param entropy
 * @return {*}
 * @constructor
 */
export default function Editor({
    onChange,
    className = '',
    theme = 'snow',
    value = '',
    placeholder = '',
    formats = [
        "header",
        "bold",
        "italic",
        "underline",
        "blockquote",
        "list",
        "bullet",
        "link",
    ],
    showToolbar = true,
    entropy = '',
    refHandler
}: EditorProps) {

    useEffect(() => {
        if(ref && ref.current) {
            refHandler(ref.current);
        }
    }, [ref]);

    /**
     * Prevent propagation on editor click.
     *
     * @param e
     */
    function onEditorClick(e) {
        e.stopPropagation();
    }

    const id = toolbarId + ('-' + entropy);

    const memoModules = useMemo(() => {

        return {
            toolbar: {
                container: `#${id}`,
                handlers: {
                    // 'block-code': codeHandler
                    // insertStar: insertStar
                }
            },
            clipboard: {
                matchVisual: false,
            }
        }
    }, [id]);

    return (
        <QuillWrapper onClick={onEditorClick}>
            <ReactQuill
                className={className}
                theme={theme}
                value={value}
                placeholder={placeholder}
                ref={ref}
                modules={memoModules}
                formats={formats}
                onChange={onChange}/>
            {showToolbar && <Toolbar toolbarId={id}/>}
        </QuillWrapper>
    );
}
