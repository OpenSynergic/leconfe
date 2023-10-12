<div class="block space-y-2 w-full">
   @if (!auth()->check())
   <h2 class="ml-1 text-lg text-heading">Login</h2>
   <div class="card card-compact bg-white border">
       <div class="card-body">
         <form wire:submit='login'>
           <div class="flex flex-col gap-2">
               <div class="space-y-2 form-control">
                   <label class="small-text">Email
                       <span class="text-red-500">*</span>
                   </label>
                   <input type="text" class="input input-bordered w-full bg-white h-8 rounded small-text"
                       placeholder="Username" wire:model='email'>
                  @error('email')
                  <span class="text-red-500 text-xs">{{ $message }}</span>
                  @enderror
               </div>

               <div class="space-y-2 form-control">
                   <label class="small-text">Password
                       <span class="text-red-500">*</span>
                   </label>
                   <input type="password" class="input input-bordered w-full bg-white h-8 rounded small-text"
                       placeholder="Password" wire:model='password'>
                  @error('password')
                  <span class="text-red-500 text-xs">{{ $message }}</span>
                  @enderror
               </div>

               <a href="#" class="small-text hover:text-primary">Forgot Password?</a>

               <div class="flex justify-end space-x-1">
                   <a
                      href="{{ url('register') }}"  class="btn btn-xs btn-primary btn-outline rounded text-white font-normal w-16 h-8">Register</a>
                   <button type="submit" class="btn btn-xs btn-primary rounded text-white font-normal w-14 h-8" wire:loading.attr='disabled'>
                       Login
                   </button>
               </div>
           </div>
         </form>
       </div>
   </div>
   @endif
</div>
