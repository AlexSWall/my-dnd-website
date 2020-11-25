import React from 'react';

import NavigationBar from './Components/NavigationBar';
import Flash from './Components/Flash';
import AdministrationPanel from './Components/AdministrationPanel';

export default function AdministrationApp({ authenticationData })
{
	return (
		<div id="pageWrapper">
			<NavigationBar authenticationData={ authenticationData } />
			<Flash flash={ flash } />
			<main>
				<div id="contentWrapper">
					<div id="content">
						<div id="mainPanelWrapper">
							<div id="mainPanel">
								<AdministrationPanel authenticationData={ authenticationData } />
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
	);
}