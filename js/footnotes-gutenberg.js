/* 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
(function (wp) {
    var { __ } = wp.i18n;
    var AwesomeFootnotedButton = function (props) {
        return wp.element.createElement(
            wp.blockEditor.RichTextToolbarButton, {
            icon: wp.element.createElement('span', { 'className': 'awesome-footnotes-admin-button' }),
            title: __('Add/remove Footnote', 'awesome-footnotes'),
            onClick: function () {
                let updatedText = '';
                let content = window.wp.richText.getTextContent(window.wp.richText.slice(props.value));
                if (content.indexOf(awef_gut.open) == -1 && content.indexOf(awef_gut.close) == -1) {
                    updatedText = wp.richText.insert(props.value, awef_gut.open + content + awef_gut.close);
                } else if (content.indexOf(awef_gut.open) != -1 && content.indexOf(awef_gut.close) != -1) {
                    updatedText = wp.richText.insert(props.value, content.replace(awef_gut.open, '').replace(awef_gut.close, ''));
                    //editor.selection.setContent(content.replace(awef_gut.open, '').replace(awef_gut.close, ''));
                }

                props.onChange(updatedText);
            },
            isActive: props.isActive,
        }
        );
    }
    wp.richText.registerFormatType(
        'awe/footnote', {
        title: 'Awesome Footnotes',
        tagName: 'mfn',
        className: null,
        edit: AwesomeFootnotedButton
    }
    );
})(window.wp);
