// assets/app.js
import './styles/app.css';
import { startStimulusApp } from '@symfony/stimulus-bridge';
import '@symfony/ux-turbo';

startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!',
    true,
    /\.[jt]sx?$/
));