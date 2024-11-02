// assets/controllers/search_controller.ts
import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */

export default class extends Controller {
    static targets = ['input', 'rows'];
    declare readonly inputTarget: HTMLInputElement;
    declare readonly rowsTargets: HTMLElement[];

    filter(): void {
        const query = this.inputTarget.value.toLowerCase();

        this.rowsTargets.forEach(row => {
            const name = row.dataset.name?.toLowerCase() || '';
            if (name.includes(query)) {
                row.classList.remove('hidden');  // Affiche la ligne
            } else {
                row.classList.add('hidden');    // Cache la ligne
            }
        });
    }
}