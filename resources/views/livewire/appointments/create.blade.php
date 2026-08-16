<div>
    @if($message === true)
        <script>
            alert('تم ارسال تفاصيل الحجز الى المستشفى وسيتم ارسال معلومات الموعد عبر الهاتف والبريد الالكتروني')
            location.reload()
        </script>
    @endif

    <form wire:submit.prevent="store">
        <div class="row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                <input type="text" name="username" wire:model.defer="name" placeholder="اسمك" required>
                <span class="icon fa fa-user"></span>
                @error('name') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                <input type="email" name="email" wire:model.defer="email" placeholder="البريد الالكتروني" required>
                <span class="icon fa fa-envelope"></span>
                @error('email') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                <label for="sectionSelect">القسم</label>
                <select class="form-select" name="section" wire:model="section" id="sectionSelect" required>
                    <option value="">-- اختار القسم --</option>
                    @foreach($sections as $sectionItem)
                        <option value="{{ $sectionItem['id'] }}">{{ $sectionItem['name'] }}</option>
                    @endforeach
                </select>
                @error('section') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                <label for="doctorSelect">الدكتور</label>
                <select name="doctor" wire:model="doctor" class="form-select" id="doctorSelect" required @if(empty($section)) disabled @endif>
                    <option value="">-- اختار الدكتور --</option>
                    @forelse($doctors as $doctorItem)
                        <option value="{{ $doctorItem['id'] }}">{{ $doctorItem['name'] }}</option>
                    @empty
                        @if($section)
                            <option value="" disabled>لا يوجد أطباء في هذا القسم</option>
                        @endif
                    @endforelse
                </select>
                @error('doctor') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="col-lg-12 col-md-6 col-sm-12 form-group">
                <input type="tel" name="phone" wire:model.defer="phone" placeholder="رقم الهاتف" required>
                <span class="icon fas fa-phone"></span>
                @error('phone') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                <textarea name="notes" wire:model.defer="notes" placeholder="ملاحظات"></textarea>
                @error('notes') <span class="text-danger d-block">{{ $message }}</span> @enderror
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                <button class="theme-btn btn-style-two" type="submit" name="submit-form" wire:loading.attr="disabled">
                    <span class="txt" wire:loading.remove>تاكيد</span>
                    <span class="txt" wire:loading>جاري الارسال...</span>
                </button>
            </div>
        </div>
    </form>
</div>
