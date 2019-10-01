// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

import {
    Alert,
    Component,
    ContentLoader,
    createElement,
    Label,
    PropTypes,
    Section,
    SectionItem,
    Translate,
} from '@plesk/plesk-ext-sdk';

import * as qs from 'query-string';
import axios from 'axios';

export default class Scan extends Component {
    static propTypes = {
        baseUrl: PropTypes.string.isRequired,
    };

    state = {
        loading: true,
        error: '',
        scanId: 0,
        report: [],
    };

    componentDidMount() {
        axios
            .get(`${this.props.baseUrl}/api/start-scan`, {
                params: {
                    domainId: this.params.site_id,
                },
            })
            .then(response => {
                this.setState({
                    scanId: response.data.scanId,
                });

                this.checkStatus();
            })
            .catch(error => {
                this.setState({
                    loading: false,
                    error: error.response.data.message,
                });
            });
    }

    params = qs.parse(location.search);

    checkStatus = () => {
        if (this.state.scanId <= 0) {
            return;
        }

        axios
            .get(`${this.props.baseUrl}/api/scan-status`, {
                params: {
                    scanId: this.state.scanId,
                },
            })
            .then(response => {
                if (response.data.finished) {
                    this.setState({
                        loading: false,
                        report: response.data.report,
                    });
                } else {
                    setTimeout(
                        function () {
                            this.checkStatus();
                        }.bind(this),
                        3000
                    );
                }
            })
            .catch(error => {
                this.setState({
                    loading: false,
                    error: error.response.data.message,
                });
            });
    };

    render() {
        if (this.state.loading) {
            return (
                <ContentLoader text={<Translate content="Scan.running"/>}/>
            );
        }

        if (this.state.error !== '') {
            return (
                <Alert intent="danger">
                    {this.state.error}
                </Alert>
            );
        }

        return (
            this.state.report.map(scanner => (
                <Section
                    key={scanner.scanner_code}
                    title={scanner.scanner_name}
                    collapsible
                >
                    {scanner.tests.map((test, key) => (
                        <SectionItem
                            key={key}
                            title={test.headline}
                        >
                            <Label intent={test.has_error ? 'danger' : 'success'}>{test.has_error ? 'FAILED' : 'PASSED'}</Label> {test.result}
                        </SectionItem>
                    ))}
                </Section>
            ))
        );
    }
}
