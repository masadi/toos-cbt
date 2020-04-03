@include('ujian.sidenav')
@include('ujian.sidenav_mini')
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card mata-ujian">
                    <div class="card-body">
                        <!--input type="button" class="increase" value=" + ">
                        <input type="button" class="decrease" value=" - " />
                        <input type="button" class="resetMe" value=" = "-->
                        <button type="button" class="btn btn-secondary increase"><span class="cil-zoom-in btn-icon"></span></button>
                        <button type="button" class="btn btn-secondary decrease"><span class="cil-zoom-out btn-icon"></span></button>
                        <button type="button" class="btn btn-secondary resetMe"><span class="cil-zoom btn-icon"></span></button>
                        <button type="button" class="btn btn-secondary refresh"><span class="cil-reload btn-icon"></span></button>
                        <div id="isi-ujian">
                        @foreach($questions as $question)
                            <input type="hidden" id="question_id" value="{{$question->question_id}}">
                            <div>
                                @if($question->soal)
                                {!!Helper::player($question->soal->question)!!}
                                @else
                                {!!Helper::player($question->question)!!}
                                @endif
                            </div>
                            <div class="btn-group-toggle" data-toggle="buttons">
                                @foreach($question->answers as $key => $answer)
                                <?php
                                $class = 'btn-secondary';
                                if($question->answer_id == $answer->answer_id){
                                    $class = 'btn-danger';
                                } else {
                                    if($question->user_question){
                                        if($question->user_question->answer_id == $answer->answer_id){
                                            $class = 'btn-danger';
                                        }
                                    }
                                }
                                ?>
                                <label class="pilihan btn btn-pill {{$class}}" for="{{$answer->answer_id}}">
                                    <input class="radio-pilihan" type="radio" name="answer_id"
                                        id="{{$answer->answer_id}}" value="{{$answer->answer_id}}"
                                        @if($question->answer_id == $answer->answer_id)
                                            checked
                                        @else
                                        @if($question->user_question)
                                            @if($question->user_question->answer_id == $answer->answer_id)
                                                checked
                                            @endif
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
                    {{-- $questions->links('vendor.pagination.ujian', ['jumlah_jawaban_siswa' => $jumlah_jawaban_siswa]) --}}
                    @include('ujian.navigasi')
                </div>
            </div>
        </div>
    </div>
</div>