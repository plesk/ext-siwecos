// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

import {
    Component,
    createElement,
    PropTypes,
} from '@plesk/plesk-ext-sdk';

export default class Overview extends Component {
    static propTypes = {
        baseUrl: PropTypes.string.isRequired,
    };

    render() {
        return (
            <div>
                {'Nothing to see here...'}
            </div>
        );
    }
}
