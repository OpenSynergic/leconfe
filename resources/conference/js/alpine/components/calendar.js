import VanillaCalendar from '@uvarov.frontend/vanilla-calendar';

// Reuse Style
import '../../../css/calendar/vanila-calendar.min.css';
import '../../../css/calendar/themes/dark.min.css';
import '../../../css/calendar/themes/light.min.css';

document.addEventListener('alpine:init', () => {
    Alpine.data('calendar', () => {
        return {
            options: {
                DOMTemplates: {
                    default: `
                      <div class="vanilla-calendar-header">
                        <div class="vanilla-calendar-header__content">
                        <#Month />
                        </div>
                        <#ArrowPrev />
                        <#ArrowNext />
                      </div>
                      <div class="vanilla-calendar-wrapper">
                        <div class="vanilla-calendar-content">
                          <#Week />
                          <#Days />
                        </div>
                      </div>
                    `,
                  },
                settings: {
                    visibility: {
                        theme: 'light',
                    },
                },
                popups: {
                    '2023-09-23': {
                        modifier: 'current_conference',
                        html: 'Conference Ardenton 2015',
                    },
                    '2023-09-24': {
                        modifier: 'upcoming_conference',
                        html: 'Conference Bussiness 2018',
                    },
                    '2023-09-25': {
                        modifier: 'upcoming_conference',
                        html: 'Conference Technology 2019',
                    },
                },
            },
            init() {
                this.initCalendar();
            },
            initCalendar() {
                const calendar = new VanillaCalendar('#calendar', this.options);
                calendar.init();
            },
        };
    });
});


