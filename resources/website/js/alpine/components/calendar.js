import VanillaCalendar from '@uvarov.frontend/vanilla-calendar';

document.addEventListener('alpine:init', () => {
    Alpine.directive('calendar', (el, { expression }, { effect, cleanup }) => {
        const timelinesData = JSON.parse(expression);
        effect(() => {
            const options = {
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
            };

            const calendar = new VanillaCalendar(el, options);
            calendar.init();
        });

        cleanup(() => {

        });
    });
});



