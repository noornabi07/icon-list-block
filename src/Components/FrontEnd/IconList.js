import Themes from '../Common/Themes';
import { prefix } from '../../utils/data';

const IconList = ({ attributes }) => {
	const { isTitle, title, isDesc, desc, lists, isListLinkInNewTab, position, isHeaderSep, themes, featureTitle } = attributes;
	const { theme = 'default' } = themes;
	return <>
		<div className={`${prefix} ${position}`}>
			{'default' === theme && <>
				{isTitle || isDesc ? <div className='header'>
					{isTitle && title && <h3 className='title' dangerouslySetInnerHTML={{ __html: title }} />}

					{isDesc && desc && <p className='description' dangerouslySetInnerHTML={{ __html: desc }} />}

					{isHeaderSep && <span className='separator' />}
				</div> : null}
			</>}

			{
				'theme3' === theme && <>
					{
						isTitle && <p className='featureHeader' dangerouslySetInnerHTML={{ __html: featureTitle }} />
					}
				</>
			}

			<ul className={`lists ${theme}`}>
				{lists?.map((list, index) => {
					const { text, link, des, featureDes, badgeTitle, theme5Text, theme5Des } = list;

					const textEl = 'theme2' === theme ? <h3 className='text' dangerouslySetInnerHTML={{ __html: text }} /> : <p className='text' dangerouslySetInnerHTML={{ __html: text }} />

					const desEl = <p className='description' dangerouslySetInnerHTML={{ __html: des }} />

					const featureDesEl = <p className='featureDescription' dangerouslySetInnerHTML={{ __html: featureDes }} />

					const theme5TextSl = <h3 className='card-title' dangerouslySetInnerHTML={{ __html: theme5Text }} />

					const theme5DesSl = <p className='card-description' dangerouslySetInnerHTML={{ __html: theme5Des }} />

					return <LinkChecker key={index} {...{ link, isListLinkInNewTab }}>
						<li className='list'>
							<Themes {...{ attributes, list, textEl, desEl, featureDesEl, badgeTitle, link, theme5TextSl, theme5DesSl }} />
						</li>
					</LinkChecker>
				})}
			</ul>
		</div>
	</>
}
export default IconList;

const LinkChecker = ({ link, isListLinkInNewTab, children }) => {
	return link ? <a className='link' href={link} target={isListLinkInNewTab ? '_blank' : '_self'} rel='noreferrer'>
		{children}
	</a> : children;
}