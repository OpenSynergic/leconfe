<div class="card card-compact">
   <div class="card-body w-full">
    <div x-data="calendar" x-init="init" class="mx-auto">
        <div id="calendar"></div>
    </div>
   </div>
  @once
    <script>
        let timelinesData = {{ Js::from($timelines) }}
    </script>
 @endonce
</div>
