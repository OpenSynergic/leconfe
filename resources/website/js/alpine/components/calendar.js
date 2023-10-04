import VanillaCalendar from '@uvarov.frontend/vanilla-calendar';

// Reuse Style
// import '../../../css/calendar/vanila-calendar.min.css';
// import '../../../css/calendar/themes/light.min.css';
import '../../../css/calendar/vanila-calendar-min.css';
import '../../../css/calendar/themes/light.min.css';


document.addEventListener('alpine:init', () => {
    Alpine.data('calendar', () => {
        // data from calendar block component
        console.log(timelinesData);
        return {
            options: {
                DOMTemplates: {
                    default: `
                      <div class="vanilla-calendar-header">
                        <div class="vanilla-calendar-header__content">
                        <#Month />  <#Year />
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
                popups: timelinesData,
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


