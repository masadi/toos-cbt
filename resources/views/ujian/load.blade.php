<div class="sidenav">
    <button type="button" class="btn btn-square btn-danger button" data-toggle="collapse" data-target="#navigasi"
        aria-expanded="false" aria-controls="navigasi"><span class="cil-contrast btn-icon mr-2"></span></button>
    <div class="collapse" id="navigasi">
        <div class="row">
            <div class="col-sm-4 navigasi-jawaban">
                <div class="card card-body">
                    <p>@foreach($ujian->question as $s)
                        <?php
                        $jawaban = $s->user_question()->where(function($query) use ($s, $user){
                            $query->where('question_id', $s->question_id);
                            $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
                        })->first();
                        ?>
                        @if($jawaban)
                        @if($jawaban->ragu)
                        <button data-url="{{$questions->url($loop->iteration)}}"
                            class="btn btn-navigasi btn-square btn-warning" type="button"
                            style="margin-bottom:5px;">{{$loop->iteration}}</button>
                        @else
                        @if($jawaban->answer_id)
                        <button data-url="{{$questions->url($loop->iteration)}}"
                            class="btn btn-navigasi btn-square btn-success" type="button"
                            style="margin-bottom:5px;">{{$loop->iteration}}</button>
                        @else
                        <button data-url="{{$questions->url($loop->iteration)}}"
                            class="btn btn-navigasi btn-square btn-secondary" type="button"
                            style="margin-bottom:5px;">{{$loop->iteration}}</button>
                        @endif
                        @endif
                        @else
                        <button data-url="{{$questions->url($loop->iteration)}}"
                            class="btn btn-navigasi btn-square btn-secondary" type="button"
                            style="margin-bottom:5px;">{{$loop->iteration}}</button>
                        @endif
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card mata-ujian">
                    <div class="card-body">
                        <input type="button" class="increase" value=" + ">
                        <input type="button" class="decrease" value=" - " />
                        <input type="button" class="resetMe" value=" = ">
                        <div id="isi-ujian">
                        @foreach($questions as $question)
                        <?php
                        $get_jawaban = $question->user_question()->where(function($query) use ($question, $user){
                            $query->where('question_id', $question->question_id);
                            $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
                        })->first();
                        ?>
                            <input type="hidden" id="question_id" value="{{$question->question_id}}">
                            <div>
                                {!!Helper::player($question->question)!!}
                            </div>
                            <div class="btn-group-toggle" data-toggle="buttons">
                                @foreach($question->answer as $key => $answer)
                                <?php
                                $class = 'btn-secondary';
                                if($get_jawaban){
                                    if($get_jawaban->answer_id == $answer->answer_id){
                                        $class = 'btn-danger';
                                    }
                                }
                                ?>
                                <label class="pilihan btn btn-pill {{$class}}" for="{{$answer->answer_id}}">
                                    <input class="radio-pilihan" type="radio" name="answer_id"
                                        id="{{$answer->answer_id}}" value="{{$answer->answer_id}}"
                                        @if($get_jawaban)
                                            @if($get_jawaban->answer_id == $answer->answer_id)
                                            checked
                                        @endif
                                        @endif
                                    >
                                    {{Helper::generateAlphabet($key)}}
                                </label>
                                <span>{!!Helper::player($answer->answer)!!}</span>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $questions->links('vendor.pagination.ujian', ['jumlah_jawaban_siswa' => $jumlah_jawaban_siswa]) }}
                </div>
            </div>
        </div>
    </div>
</div>