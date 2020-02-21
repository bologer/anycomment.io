import React, {Context} from "react";
import {ContextValueProps} from "./AnyCommentProvider";

const AnyCommentContext: Context<ContextValueProps> = React.createContext<ContextValueProps>({});

AnyCommentContext.displayName = 'AnyCommentContext';

export default AnyCommentContext;
