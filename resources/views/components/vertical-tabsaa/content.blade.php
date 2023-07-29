<section 
  x-bind="tabcontent"
  role="tabpanel" 
  x-cloak
  {{ $attributes->class(['focus:outline-none h-full border pr-4']) }}
  >
  {{ $slot }}
</section>