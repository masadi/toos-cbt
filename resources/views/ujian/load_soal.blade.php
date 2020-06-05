<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card mata-ujian">
                    <div class="card-body">
                        <!--input type="button" class="increase" value=" + ">
                        <input type="button" class="decrease" value=" - " />
                        <input type="button" class="resetMe" value=" = "-->
                        <button type="button" class="btn btn-success increase"><span class="fas fa-search-plus"></span></button>
                        <button type="button" class="btn btn-success decrease"><span class="fas fa-search-minus"></span></button>
                        <button type="button" class="btn btn-success resetMe"><span class="fas fa-font"></span></button>
                        <button type="button" class="btn btn-success refresh"><span class="fas fa-sync-alt"></span></button>
                        <div id="isi-ujian">
                        @foreach($questions as $question)
                            <input type="hidden" id="question_id" value="{{$question->question_id}}">
                            <div>
                                {{--Helper::player($question->question)--}}
                                {!! $question->question !!}
                            </div>
                            <div class="btn-group-toggle" data-toggle="buttons">
                                @foreach($question->answers as $key => $answer)
                                <?php
                                $class = 'btn-secondary';
                                $answer_id = NULL;
                                $jawaban_id = NULL;
                                if($jawaban_siswa){
                                    $answer_id = $jawaban_siswa->answer_id;
                                    $jawaban_id = $jawaban_siswa->question_id;
                                    if($jawaban_siswa->answer_id == $answer->answer_id){
                                        $class = 'btn-danger';
                                    }
                                }
                                /*if($question->answer_id == $answer->answer_id){
                                    $class = 'btn-danger';
                                } else {
                                    if($question->user_question){
                                        if($question->user_question->answer_id == $answer->answer_id){
                                            $class = 'btn-danger';
                                        }
                                    }
                                }*/
                                //echo $jawaban_id. '=>'. $question->question_id. '=>'. $current_id;
                                ?>
                                <label class="pilihan btn btn-pill {{$class}}" for="{{$answer->answer_id}}">
                                    <input class="radio-pilihan" type="radio" name="answer_id"
                                        id="{{$answer->answer_id}}" value="{{$answer->answer_id}}"
                                        @if($jawaban_siswa)
                                            @if($jawaban_siswa->answer_id == $answer->answer_id){
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
                    <div class="card-footer">
                        {{-- $questions->links('vendor.pagination.ujian', ['jumlah_jawaban_siswa' => $jumlah_jawaban_siswa]) --}}
                        @include('ujian.navigasi', ['jawaban_siswa' => $jawaban_siswa])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>