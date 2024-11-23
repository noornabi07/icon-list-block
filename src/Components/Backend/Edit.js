import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { produce } from 'immer';
import { useEffect, useState } from 'react';
import { tabController } from '../../../../bpl-tools/utils/functions';
import Style from '../Common/Style';
import Settings from './Settings/Settings';
import Themes from '../Common/Themes';
import { prefix } from '../../utils/data';

const Edit = props => {
	const { attributes, setAttributes, clientId, isSelected } = props;
	const { isTitle, title, isDesc, desc, lists, position, isHeaderSep, themes, featureTitle } = attributes;
	const { theme } = themes;

	useEffect(() => tabController(), [isSelected]);

	const [activeIndex, setActiveIndex] = useState(0);

	const updateList = (type, val, otherType = false) => {
		const newLists = produce(lists, draft => {
			if (otherType) {
				draft[activeIndex][type][otherType] = val;
			} else {
				draft[activeIndex][type] = val;
			}
		});
		setAttributes({ lists: newLists });
	}

	const id = `${prefix}-${clientId}`;

	return <>
		<Settings attributes={attributes} setAttributes={setAttributes} updateList={updateList} activeIndex={activeIndex} setActiveIndex={setActiveIndex} />

		<div {...useBlockProps()} id={id}>
			<Style attributes={attributes} id={id} />


			<div className={`${prefix} ${position}`}>
				{'default' === theme && <>
					{isTitle || isDesc ? <div className='header'>
						{isTitle && <RichText className='title' tagName='h3' value={title} onChange={val => setAttributes({ title: val })} placeholder={__('Title', 'icon-list')} inlineToolbar />}

						{isDesc && <RichText className='description' tagName='p' value={desc} onChange={val => setAttributes({ desc: val })} placeholder={__('Description', 'icon-list')} inlineToolbar />}

						{isHeaderSep && <span className='separator' />}
					</div> : null}
				</>}

				{
					'theme3' === theme && <>
						{
							isTitle && <>
								<RichText className='featureHeader' tagName='p' value={featureTitle} onChange={val => setAttributes({ featureTitle: val })} placeholder={__('Feature Title', 'icon-list')} inlineToolbar />
							</>
						}
					</>
				}

				<ul className={`lists ${theme}`}>
					{lists?.map((list, index) => {
						const { text, des, featureDes, badgeTitle, link, theme5Text, theme5Des } = list;

						const textEl = <RichText className='text' tagName={'theme2' === theme ? 'h3' : 'p'} value={text} onChange={val => updateList('text', val)} placeholder={__('Text', 'icon-list')} inlineToolbar allowedFormats={['core/bold', 'core/italic', 'core/link']} />

						const desEl = <RichText className='description' tagName='p' value={des} onChange={val => updateList('des', val)} placeholder={__("Type Your Description", "icon-list")} inlineToolbar allowedFormats={['core/semibold', 'core/italic', 'core/link']} />

						const featureDesEl = <RichText className='featureDescription' tagName='p' value={featureDes} onChange={val => updateList('featureDes', val)} placeholder={__("Feature Description", "icon-list")} inlineToolbar allowedFormats={['core/semibold', 'core/italic', 'core/link']} />

						const theme5TextSl = <RichText className='card-title' tagName='h3' value={theme5Text} onChange={val => updateList('theme5Text', val)} placeholder={__("Type Your Title", "icon-list")} inlineToolbar allowedFormats={['core/semibold', 'core/italic', 'core/link']} />

						const theme5DesSl = <RichText className='card-description' tagName='p' value={theme5Des} onChange={val => updateList('theme5Des', val)} placeholder={__("Type Your Description", "icon-list")} inlineToolbar allowedFormats={['core/semibold', 'core/italic', 'core/link']} />


						return <li key={index} className={`list ${index === activeIndex ? 'ilbNowEditing' : ''}`} onClick={() => setActiveIndex(index)}>
							<Themes {...{ attributes, list, textEl, desEl, featureDesEl, badgeTitle, link, theme5TextSl, theme5DesSl }} />
						</li>
					})}
				</ul>
			</div>
		</div>
	</>;
};
export default Edit;