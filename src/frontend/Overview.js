// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

import {
    Component,
    createElement,
    PropTypes,
    Translate,
} from '@plesk/plesk-ext-sdk';

export default class Overview extends Component {
    static propTypes = {
        baseUrl: PropTypes.string.isRequired,
    };

    render() {
        return (
            <div>
                <h3><Translate content="Overview.descriptionSubject"/></h3>
                <p><Translate content="Overview.description"/></p>
            </div>
        );
    }
}
